select	u.usuario_nome,
			(
				select	ifnull(sum(a1.aposta_vencedora_valor), 0)
				from		aposta a1
				where		a1.aposta_vencedora = 1
							and a1.usuario_id = u.usuario_id
			) as premio,
			(
				select	ifnull(sum(p.partida_valor), 0)
				from		aposta a2
							inner join partida p on a2.partida_id = p.partida_id
				where		(a2.aposta_vencedora = 0 or a2.aposta_vencedora is null)
							and a2.usuario_id = u.usuario_id
			) as apostas,
			(
				select	ifnull(sum(a1.aposta_vencedora_valor), 0)
				from		aposta a1
				where		a1.aposta_vencedora = 1
							and a1.usuario_id = u.usuario_id
			) - 
			(
				select	ifnull(sum(p.partida_valor), 0)
				from		aposta a2
							inner join partida p on a2.partida_id = p.partida_id
				where		(a2.aposta_vencedora = 0 or a2.aposta_vencedora is null)
							and a2.usuario_id = u.usuario_id
			) as saldo
from		usuario u
group by u.usuario_id
order by (
				select	ifnull(sum(a1.aposta_vencedora_valor), 0)
				from		aposta a1
				where		a1.aposta_vencedora = 1
							and a1.usuario_id = u.usuario_id
			) - 
			(
				select	ifnull(sum(p.partida_valor), 0)
				from		aposta a2
							inner join partida p on a2.partida_id = p.partida_id
				where		(a2.aposta_vencedora = 0 or a2.aposta_vencedora is null)
							and a2.usuario_id = u.usuario_id
			) desc